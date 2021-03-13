using System;
using System.Threading.Tasks;
using Kracker.Base.Services.Model.Jobs;

namespace Kracker.Base.Domain.Jobs
{
    public interface IJobHandler
    {
        void Execute();
        bool IsCompleted();
        JobDescription GetJobDescription();
        void Cancel();
        Task Finish();
        Task Finish(Exception exception);
    }
}