using System;
using System.Text.Json.Serialization;

namespace Kracker.Base.Domain.Inventory
{
    public class FileDescription
    {
        public long Id { get; set; }
        [JsonPropertyName("name")] public string Name { get; set; } = string.Empty;

        [JsonPropertyName("size")] public long Size { get; set; }

        [JsonPropertyName("count")]
        public long LinesCount { get; set; }

        [JsonPropertyName("checksum")] 
        public string Сhecksum { get; set; }= string.Empty;

        [JsonPropertyName("type")]
        public string FolderName { get; set; } = string.Empty;

        [JsonPropertyName("lastwritetime")]
        public DateTime LastWriteTime { get; set; }
    }
}