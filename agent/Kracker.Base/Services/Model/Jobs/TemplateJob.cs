using System.Text.Json.Serialization;

namespace Kracker.Base.Services.Model.Jobs
{
    public abstract record TemplateJob(long TemplateId, JobType Type):AbstractJob(Type)
    {
        [JsonPropertyName("template_id")]
        public long TemplateId { get; init; } = TemplateId;
    }
}