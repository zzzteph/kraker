using System.Text.Json.Serialization;
using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record BruteforceJob(long JobId,
        string HashListId,
        int HashTypeId,
        long Skip,
        long Limit,
        string Mask,
        string Charset1,
        string Charset2,
        string Charset3,
        string Charset4,
        string Content,
        string PotContent) : AbstractJob(JobType.Bruteforce)
    {
        [JsonPropertyName("job_id")] 
        public long JobId { get; init; } = JobId;

        [JsonPropertyName("hashlist_id")] 
        public string HashListId { get; init; } = HashListId;

        [JsonPropertyName("hashtype_id")] 
        public int HashTypeId { get; init; } = HashTypeId;

        [JsonPropertyName("pot_content")] 
        public string? PotContent { get; init; } = PotContent;
        public override JobDescription GetDescription() 
            => new (Bruteforce, JobId);
    }
}