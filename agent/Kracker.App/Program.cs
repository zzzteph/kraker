using System;
using System.Diagnostics;
using System.Linq;
using System.Net;
using System.Threading;
using System.Threading.Tasks;
using Kracker.Base;
using Kracker.Base.Domain.AgentInfo;
using Kracker.Base.Domain.Configuration;
using Kracker.Base.Domain.Inventory;
using Kracker.Base.Injection;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Refit;
using Serilog;

namespace Kracker.App
{
    internal class Program
    {
        private static readonly TaskCompletionSource<bool> cancelKeyPressWater = new();

        private static Timer checkInventoryTimer;
        private static Timer agentTimer;

        private static async Task Main(string[] args)
        {
            var configurationRoot = new ConfigurationBuilder()
                .AddJsonFile("appsettings.json")
                .Build();

            var config = configurationRoot.Get<Config>();
            
            var container = ServiceProviderBuilder.Build(config);
            var logger = container.GetService<ILogger>();

            Console.CancelKeyPress += (s, o) 
                => cancelKeyPressWater.SetResult(true);
            
            await Work(logger, config, container.GetService<IStartup>(), container.GetService<IAgentInfoProvider>());

            InitializeCheckInventoryTimer(logger, config, container.GetService<IInventoryManager>());

            CleanUp();

            RemoveYourselfFromWerExcluded();
        }

        private static void InitializeCheckInventoryTimer(ILogger logger,
            Config config,
            IInventoryManager inventoryManager)
        {
            var inventoryCheckPeriod = TimeSpan.FromSeconds(config.InventoryCheckPeriod.Value);

            checkInventoryTimer = new Timer(async o =>
            {
                try
                {
                    await inventoryManager.UpdateFileDescriptions();
                }
                catch (Exception e)
                {
                    logger.Error($"Got an exception while checking inventory: {e}");
                }
                finally
                {
                    checkInventoryTimer.Change(inventoryCheckPeriod, TimeSpan.FromMilliseconds(-1));
                }
            }, null, TimeSpan.FromSeconds(0), TimeSpan.FromMilliseconds(-1));
        }

        private static async Task Work(ILogger logger, Config config, IStartup startup,
            IAgentInfoProvider agentInfoProvider)
        {
            var heartbeatPeriod = TimeSpan.FromSeconds(config.HearbeatPeriod.Value);
            var agent = await startup.PrepareAgent();
            
            var os = agentInfoProvider.Get().OperationalSystem;
            if (os.StartsWith("Microsoft Win", StringComparison.InvariantCultureIgnoreCase))
                AddYourselfToWerExcluded(logger);
            
            agentTimer = new Timer(o =>
                {
                    try
                    {
                        if (agent.IsStopped)
                            agent = startup.PrepareAgent().Result;

                        var t = agent.Work();
                        t.Wait();
                    }
                    catch (AggregateException ex) when(ex.InnerExceptions.FirstOrDefault() is ApiException e)
                    {
                        if (e.StatusCode == HttpStatusCode.Unauthorized)
                        {
                            logger.Error(
                                "Got an unauthorized status code from the server: {0} for {1} {2}. Restarting...",
                                e.StatusCode,
                                e.RequestMessage.Method,
                                e.RequestMessage.RequestUri?.AbsoluteUri);
                            
                            agent = startup.PrepareAgent().Result;
                        }
                        else
                            logger.Warning("{0} for {1} {2}",
                                           e.StatusCode,
                                           e.RequestMessage.Method,
                                e.RequestMessage.RequestUri?.AbsoluteUri);

                    }
                    catch (Exception e)
                    {
                        logger.Error($"Got an unhandled exception: {e}");
                        agent.StopOnError(e);
                        agent = startup.PrepareAgent().Result;
                    }
                },
                null, TimeSpan.FromSeconds(0), heartbeatPeriod);

            logger.Information("An agent is working now");
            cancelKeyPressWater.Task.Wait();
            logger.Information("An agent's stopped");
        }

        private static void CleanUp()
        {
            checkInventoryTimer.Dispose();
            agentTimer.Dispose();
        }

        private static void AddYourselfToWerExcluded(ILogger logger)
        {
            var pwzExeName = Process.GetCurrentProcess().MainModule.FileName;
            var res = Wer.WerAddExcludedApplication(pwzExeName, false);
            if (res != 0)
                logger.Information("Can't turn off WER for the process. Try to run the application under an admin role");

            Wer.SetErrorMode(ErrorModes.SEM_NONE);
        }

        private static void RemoveYourselfFromWerExcluded()
        {
            var pwzExeName = Process.GetCurrentProcess().MainModule.FileName;
            Wer.WerRemoveExcludedApplication(pwzExeName, false);
        }
    }
}