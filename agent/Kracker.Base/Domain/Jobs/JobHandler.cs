using System;
using System.Threading.Tasks;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Domain.HashCat;
using Kracker.Base.Services;
using Kracker.Base.Services.Model.Jobs;

namespace Kracker.Base.Domain.Jobs
{
    public abstract class JobHandler<T> : IJobHandler where T:AbstractJob
    {
        protected readonly string _agentId;
        protected readonly T _job;
        protected readonly TempFilePaths _paths;
        protected readonly IKrakerApi _krakerApi;
        protected readonly HashCatCommandExecutor _executor;
        
        protected Task<ExecutionResult> _hashCatTask;

        public JobHandler(
            T job,
            string agentId,
            TempFilePaths paths,
            IHashCatCommandExecutorBuilder executorBuilder,
            IKrakerApi krakerApi)
        {
            _job = job;
            _agentId = agentId;
            _krakerApi = krakerApi;
            _paths = paths;
            _executor = executorBuilder.Build(job, paths);
            _hashCatTask = Task.FromResult(ExecutionResult.FromError(0,"There isn't a task"));
        }

        public void Execute()
        {
            _hashCatTask = _executor.Execute();
        }

        public bool IsCompleted()
        {
            return _hashCatTask.IsCompleted;
        }

        public JobDescription GetJobDescription()
        {
            return _job.GetDescription();
        }

        public void Cancel()
        {
            _executor.CancellationToken.Cancel();
        }

        public abstract Task Finish();
        public abstract Task Finish(Exception exception);
    }
}