using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record IncorrectJob(string Error) : AbstractJob(JobType.IncorrectJob)
    {
        public override JobDescription GetDescription()
            => new(Incorrect, "");
    }
}