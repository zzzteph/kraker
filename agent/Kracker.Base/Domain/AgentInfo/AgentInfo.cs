using System.Text.Json.Serialization;

namespace Kracker.Base.Domain.AgentInfo
{
    public record AgentInfo
    {
        [JsonPropertyName("ip")] 
        public string? Ip { get; set; }

        [JsonPropertyName("hostname")]
        public string? HostName { get; set; }

        [JsonPropertyName("os")] 
        public string? OperationalSystem { get; set; }

        [JsonPropertyName("hashcat")]
        public string? HashcatVersion { get; set; }

        [JsonPropertyName("hw")] 
        public string? HardwareInfo { get; set; }
    }
}