using System;
using System.Threading.Tasks;
using Kracker.Base.Domain.AgentId;
using Kracker.Base.Services;
using Kracker.Base.Services.Model.Jobs;
using Kracker.Base.Services.Model.Responses;
using Kracker.Base.Tools;
using Serilog;

namespace Kracker.Base.Domain.Jobs
{
    public interface IAgent
    {
        bool IsStopped { get; }

        Task Work();
        Task StopOnError(Exception exception);
    }

    public class Agent : IAgent
    {
        private readonly string _agentId;
        private readonly IJobHandler _incorrectJobHandler;
        private readonly IJobHandlerProvider _jobHandlerProvider;
        private readonly IKrakerApi _krakerApi;
        private readonly ILogger _logger;
        private readonly FiniteStateMachine _switch;
        private IJobHandler _jobHandler;

        public Agent(IJobHandlerProvider jobHandlerProvider,
            IAgentIdManager agentIdManager,
            IKrakerApi krakerApi,
            ILogger logger)
        {
            _switch = new FiniteStateMachine(WaitJob);
            _jobHandlerProvider = jobHandlerProvider;
            _krakerApi = krakerApi;
            _logger = logger;
            _agentId = agentIdManager.GetCurrent().Id
                       ?? throw new InvalidOperationException("The agent needs to have id");

            var incorrectJobHandler = new IncorrectJobHandler(new IncorrectJob("Haven't got any jobs"));
            _jobHandler = incorrectJobHandler;
            _incorrectJobHandler = incorrectJobHandler;
        }

        public bool IsStopped { get; private set; }

        public async Task Work()
        {
            await Try.Do(() => _switch.RunAction(),
                exception =>
                {
                    _switch.SetStateAction(WaitJob);
                    throw exception;
                });
        }

        public async Task StopOnError(Exception exception)
        {
            IsStopped = true;
            await Try.Do(() => _jobHandler.Finish(exception),
                e => _logger.Error("Can't finish the work: {0} {1}",
                    Environment.NewLine, e));
        }

        private async Task WaitJob()
        {
            _switch.SetStateAction(DoNothing);

            var job = await _krakerApi.GetJob(_agentId);

            _logger.Information("Got a job {0}", job);

            if (job == null || job is IncorrectJob or DoNothingJob)
            {
                _switch.SetStateAction(WaitJob);
                return;
            }

            _jobHandler = _jobHandlerProvider.Get(job);

            _jobHandler.Execute();
            _switch.SetStateAction(ProcessJob);
        }

        private Task DoNothing()
        {
            return Task.CompletedTask;
        }

        private async Task ProcessJob()
        {
            _switch.SetStateAction(DoNothing);

            if (!_jobHandler.IsCompleted())
            {
                var heartbeat =
                    await Try.Do(()=>
                     _krakerApi.SendAgentStatus(_agentId, _jobHandler.GetJobDescription()),
                        e=>
                        {
                            _logger.Warning("Can't sent the status: {0}", e);
                            return WorkStatus.Continue;
                        });
                
                if (heartbeat.Status == Constants.WorkStatuses.Stop)
                {
                    _logger.Information("The job is canceled");
                    _jobHandler.Cancel();
                }

                _switch.SetStateAction(ProcessJob);
                return;
            }

            await _jobHandler.Finish();

            _jobHandler = _incorrectJobHandler;
            _switch.SetStateAction(WaitJob);
        }
    }
}