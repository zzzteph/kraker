using System.IO;
using Kracker.Base.Domain.Folders;

namespace Kracker.Base.Domain.AgentId
{
    public interface IAgentIdManager
    {
        AgentId GetCurrent();
        AgentId GetFromFile();
        void Save(string agentId);
    }

    public class AgentIdManager : IAgentIdManager
    {
        private readonly string _agentIdFilePath;
        private AgentId _current;

        public AgentIdManager(AppFolder appFolder)
        {
            _agentIdFilePath = Path.Combine(appFolder.Value, Constants.ArtefactsFolder, Constants.AgentIdFile);
            _current = GetFromFile();
        }

        public AgentId GetCurrent()
        {
            return _current;
        }

        public AgentId GetFromFile()
        {
            var agentId = File.Exists(_agentIdFilePath) 
                ? new AgentId(File.ReadAllText(_agentIdFilePath))
                : new AgentId(null);
            
            _current = agentId;
            return agentId;
        }
            

        public void Save(string agentId)
        {
            if (agentId is "")
                File.Delete(_agentIdFilePath);
            
            _current = new AgentId(agentId);
            File.WriteAllText(_agentIdFilePath,agentId);
        }
    }
}