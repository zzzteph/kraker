using System;
using System.IO;
using System.Net.Http;
using Kracker.Base.Domain.Configuration;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Domain.Jobs;
using Kracker.Base.Services;
using Microsoft.Extensions.DependencyInjection;
using Polly;
using Polly.Extensions.Http;
using Refit;
using Serilog;

namespace Kracker.Base.Injection
{
    public static class ServiceCollectionExtensions
    {
        public static IServiceCollection RegisterAppDirectory(this IServiceCollection services) =>
            services.AddSingleton(new AppFolder(Directory.GetCurrentDirectory()));
        
        public static IServiceCollection RegisterKrakerApi(this IServiceCollection services, Config config)
        {
            services.AddRefitClient<IKrakerApi>()
                .ConfigureHttpClient(client => { client.BaseAddress = new Uri(config.ServerUrl); })
                .AddPolicyHandler(HttpPolicyExtensions
                    .HandleTransientHttpError()
                    .Or<HttpRequestException>()
                    //.OrResult(responce=> (int) responce.StatusCode >= 400)
                    .WaitAndRetryAsync(3,
                        attempt => TimeSpan.FromMilliseconds(300),
                        (ex, span) => Log.Error("{0} for {1} {2}. {3} ",
                            ex.Result.StatusCode, ex.Result.RequestMessage.Method,
                            ex.Result.RequestMessage.RequestUri?.AbsoluteUri,
                            ex.Exception)));

            return services;
        }
        
        public static IServiceCollection RegisterConfig(this IServiceCollection services, Config config) 
            => services.AddSingleton(config);

        public static IServiceCollection RegisterAllTypesAsSingleton(this IServiceCollection services) =>
            services
                .Scan(scan => scan.FromAssemblyOf<IKrakerApi>()
                    .AddClasses()
                    .AsImplementedInterfaces()
                    .WithSingletonLifetime()
                );

        public static IServiceCollection RegisterLogging(this IServiceCollection services)
        {
            var logger = new LoggerConfiguration()
                .WriteTo.Console()
                .WriteTo.RollingFile(Path.Combine("Logs", "log_{Date}.txt"), retainedFileCountLimit: 7)
                .CreateLogger();
            
            Log.Logger = logger;
            
            services.AddSingleton<ILogger>(logger);
            return services;
        }
    }
    
    
}