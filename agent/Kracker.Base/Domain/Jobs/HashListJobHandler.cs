using System;
using System.Linq;
using System.Threading.Tasks;
using Kracker.Base.Domain.HashCat;
using Kracker.Base.Services;
using Kracker.Base.Services.Model.Jobs;
using Kracker.Base.Services.Model.Responses;

namespace Kracker.Base.Domain.Jobs
{
    public class HashListJobHandler : JobHandler<HashListJob>
    {
        private readonly ITempFileManager _tempFileManager;

        public HashListJobHandler(
            IKrakerApi krakerApi,
            string agentId,
            string tempFilesDirectoryPath,
            ITempFileManager tempFileManager,
            HashListJob job,
            IHashCatCommandExecutorBuilder executorBuilder) : base(
            job,
            agentId,
            tempFileManager.BuildTempFilePaths(tempFilesDirectoryPath),
            executorBuilder,
            krakerApi)
        {
            _tempFileManager = tempFileManager;

            _tempFileManager.WriteBase64Content(_paths.HashFile, _job.Content);
            _tempFileManager.WriteBase64Content(_paths.PotFile, string.Empty);
        }

        public override async Task Finish()
        {
            var executionResult = _hashCatTask.Result;
            var error = executionResult.Errors.FirstOrDefault(e =>
                e.Contains("No hashes loaded") || e.Contains("Unhandled Exception"));

            _tempFileManager.DeleteTemFiles(_paths);

            if (error != null)
                if (error.Contains("No hashes loaded"))
                    await _krakerApi.SendHashList(_agentId,
                        _job.HashListId,
                        new HashListResponse(0, "No hashes loaded", executionResult.ExecutionTime));
                else
                    await _krakerApi.SendHashList(_agentId,
                        _job.HashListId,
                        new HashListResponse(0, error, executionResult.ExecutionTime));
            else
                await _krakerApi.SendHashList(_agentId,
                    _job.HashListId,
                    new HashListResponse(executionResult.Output.Count, null, executionResult.ExecutionTime));
        }

        public override Task Finish(Exception exception)
        => _krakerApi.SendHashList(_agentId, _job.HashListId,
            new HashListResponse(0, exception.Message, 0));

    }
}