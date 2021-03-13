using System.Text.Json.Serialization;
using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record WordListJob(long JobId,
        string HashListId,
        int HashTypeId,
        long Skip,
        long Limit,
        long WordlistId,
        long? RuleId,
        string Content,
        string PotContent) : AbstractJob(JobType.WordList)
    {
        [JsonPropertyName("job_id")]
        public long JobId { get; init; } = JobId;

        [JsonPropertyName("hashlist_id")] 
        public string HashListId { get; init; } = HashListId;

        [JsonPropertyName("hashtype_id")] 
        public int HashTypeId { get; init; } = HashTypeId;

        [JsonPropertyName("pot_content")] 
        public string? PotContent { get; init; } = PotContent;
        
        [JsonPropertyName("wordlist_id")]
        public long WordlistId { get; init; } = WordlistId;

        [JsonPropertyName("rule_id")]
        public long? RuleId { get; init; } = RuleId;

        public override JobDescription GetDescription()
            => new(Wordlist, JobId);
    }
}