using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record DoNothingJob() : AbstractJob(JobType.DoNothing)
    {
        public override JobDescription GetDescription() 
            => new(DoNothing, string.Empty);
    }
}