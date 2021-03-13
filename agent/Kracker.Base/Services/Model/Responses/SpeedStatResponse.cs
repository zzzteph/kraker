using System.Text.Json.Serialization;

namespace Kracker.Base.Services.Model.Responses
{
    public record SpeedStatResponse(long HashTypeId, string Speed, long time)
    {
        [JsonPropertyName("hashtype_id")]
        public long HashTypeId { get; init; } = HashTypeId;
    }
}