using System.Text.Json.Serialization;
using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record TemplateWordListJob(long TemplateId,
        long WordlistId,
        long? RuleId) : TemplateJob(TemplateId, JobType.TemplateWordlist)
    {
        [JsonPropertyName("wordlist_id")]
        public long WordlistId { get; init; } = WordlistId;
        
        [JsonPropertyName("rule_id")]
        public long? RuleId { get; init; } = RuleId;

        public override JobDescription GetDescription()
            => new(TemplateWordlist, TemplateId);
    }
}