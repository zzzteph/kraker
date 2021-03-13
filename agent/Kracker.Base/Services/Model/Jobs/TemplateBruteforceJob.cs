using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record TemplateBruteforceJob(long TemplateId,
        string Mask,
        string Charset1,
        string Charset2,
        string Charset3,
        string Charset4) : TemplateJob(TemplateId, JobType.TemplateBruteforce)
    {
        public override JobDescription GetDescription()
            => new(TemplateBruteforce, TemplateId);
    }
}