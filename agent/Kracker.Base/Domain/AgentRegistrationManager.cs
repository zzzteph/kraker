using System;
using System.Threading.Tasks;
using Kracker.Base.Domain.AgentId;
using Kracker.Base.Domain.AgentInfo;
using Kracker.Base.Services;

namespace Kracker.Base.Domain
{
    public interface IAgentRegistrationManager
    {
        Task Register();
    }

    public class AgentRegistrationManager : IAgentRegistrationManager
    {
        private readonly IKrakerApi _krakerApi;
        private readonly IAgentInfoManager _agentInfoManager;
        private readonly IAgentInfoProvider _agentInfoProvider;
        private readonly IAgentIdManager _agentIdManager;

        public AgentRegistrationManager(
            IKrakerApi krakerApi,
            IAgentInfoManager agentInfoManager,
            IAgentInfoProvider agentInfoProvider,
            IAgentIdManager agentIdManager)
        {
            _krakerApi = krakerApi;
            _agentInfoManager = agentInfoManager;
            _agentInfoProvider = agentInfoProvider;
            _agentIdManager = agentIdManager;
        }

        public async Task Register()
        {
            var (registrationIsNeeded, agentId, agentInfo) = RegistrationIsNeeded();

            if (!registrationIsNeeded)
            {
                var startAgentResponse = await _krakerApi.StartAgent(agentId);
                if (startAgentResponse.IsSuccessStatusCode)
                    return;
            }
                
            agentId = (await _krakerApi.RegisterAgent()).Id
                ?? throw new InvalidOperationException("Got agent id == null");
            
            await _krakerApi.SendAgentInfo(agentId, agentInfo);
            
            _agentIdManager.Save(agentId);
            
            _agentInfoManager.Save(agentInfo);
        }

        private (bool IsNeeded, string? AgentId, AgentInfo.AgentInfo ActualAgentInfo)
            RegistrationIsNeeded()
        {
            var agentId = _agentIdManager.GetFromFile();
            
            var actualAgentInfo = _agentInfoProvider.Get();
            
            if (agentId.Id is null or "")
                return (true, agentId.Id, actualAgentInfo);

            var oldAgentInfo = _agentInfoManager.GetFromFile();

            if (oldAgentInfo is null)
                return (true, agentId.Id, actualAgentInfo);

             if (oldAgentInfo.OperationalSystem != actualAgentInfo.OperationalSystem
                || oldAgentInfo.HostName != actualAgentInfo.HostName
                || oldAgentInfo.Ip != actualAgentInfo.Ip)
                return (true, agentId.Id, actualAgentInfo);

            return (false, agentId.Id,  actualAgentInfo);
        }
    }
}