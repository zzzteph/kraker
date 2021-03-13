using Kracker.Base.Domain.AgentId;
using Kracker.Base.Services;
using Serilog;

namespace Kracker.Base.Domain.Jobs
{
    public interface IAgentBuilder
    {
        IAgent Build();
    }
    
    public class AgentBuilder : IAgentBuilder
    {
        private readonly IJobHandlerProvider _jobHandlerProvider;
        private readonly IAgentIdManager _agentIdManager;
        private readonly IKrakerApi _krakerApi;
        private readonly ILogger _logger;

        public AgentBuilder(IJobHandlerProvider jobHandlerProvider,
            IAgentIdManager agentIdManager,
            IKrakerApi krakerApi,
            ILogger logger)
        {
            _jobHandlerProvider = jobHandlerProvider;
            _agentIdManager = agentIdManager;
            _krakerApi = krakerApi;
            _logger = logger;
        }

        public IAgent Build() => new Agent(_jobHandlerProvider,
            _agentIdManager,
            _krakerApi,
            _logger);
    }
}