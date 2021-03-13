using System;

namespace Kracker.Base.Domain.AgentInfo
{
    public interface IAgentInfoProvider
    {
        AgentInfo Get();
    }

    public class AgentInfoProvider : IAgentInfoProvider
    {
        private readonly Lazy<AgentInfo> _agentInfo;

        public AgentInfoProvider(IAgentInfoManager manager)
        {
            _agentInfo = new Lazy<AgentInfo>(()=>manager.Build().Result);
        }
        
        public AgentInfo Get() => _agentInfo.Value;
    }
}