using System.Text.Json.Serialization;
using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record SpeedStatJob(long HashTypeId) : AbstractJob(JobType.SpeedStat)
    {
        [JsonPropertyName("hashtype_id")]
        public long HashTypeId { get; init; } = HashTypeId;

        public override JobDescription GetDescription()
            => new(SpeedStat, HashTypeId);
    }
}