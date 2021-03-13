using System;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Text.Json;
using System.Threading.Tasks;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Domain.HashCat;
using Kracker.Base.Tools;
using Serilog;
using static Kracker.Base.Domain.Constants;

namespace Kracker.Base.Domain.AgentInfo
{
    public interface IAgentInfoManager
    {
        Task<AgentInfo> Build();
        AgentInfo GetFromFile();
        void Save(AgentInfo agentInfo);
    }

    public class AgentInfoManager : IAgentInfoManager
    {
        private readonly string _agentInfoFilePath;
        private readonly ILogger _logger;
        private readonly IHashCatCommandExecutorBuilder _executorBuilder;

        public AgentInfoManager(ILogger logger, 
            AppFolder appFolder, 
            IHashCatCommandExecutorBuilder executorBuilder)
        {
            _logger = logger;
            _executorBuilder = executorBuilder;
            _agentInfoFilePath = Path.Combine(appFolder.Value, ArtefactsFolder, AgentInfoFile);
        }

        public AgentInfo? GetFromFile()
        {
            if (!File.Exists(_agentInfoFilePath))
                return null;
            try
            {
                var agentInfo = JsonSerializer
                    .Deserialize<AgentInfo>(File.ReadAllText(_agentInfoFilePath));

                return agentInfo;
            }
            catch (Exception e)
            {
                _logger.Error(e, "Can't read the agent info file");

                return null;
            }
        }

        public void Save(AgentInfo agentInfo) =>
            File.WriteAllText(_agentInfoFilePath,
                JsonSerializer.Serialize(agentInfo));

        public async Task<AgentInfo> Build()
        {
            var hostName = Dns.GetHostName();

            var hw = (await _executorBuilder.Build("-I")
                .Execute(true)).Output;

            var ip = Dns.GetHostAddresses(hostName)
                .FirstOrDefault(a => a.AddressFamily == AddressFamily.InterNetwork)
                ?.ToString();

            var hashcatVersion =
                await _executorBuilder.Build("-V")
                    .Execute();

            var os = Environment.OSVersion.VersionString;

            return new AgentInfo
                {
                    Ip = ip,
                    HostName = hostName,
                    OperationalSystem = os,
                    HashcatVersion = hashcatVersion.Output[0],
                    HardwareInfo = string.Join(Environment.NewLine, hw)
                };
        }
    }
}