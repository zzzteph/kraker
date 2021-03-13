using System;
using System.IO;
using System.Linq;
using System.Threading.Tasks;
using Kracker.Base.Domain.HashCat;
using Kracker.Base.Services;
using Kracker.Base.Services.Model.Jobs;
using Kracker.Base.Services.Model.Responses;
using Serilog;

namespace Kracker.Base.Domain.Jobs
{
    public class BruteforceJobHandler : JobHandler<BruteforceJob>
    {
        private readonly ISpeedCalculator _speedCalculator;
        private readonly ITempFileManager _tempFileManager;
        private readonly ILogger _logger;

        public BruteforceJobHandler(
            IKrakerApi krakerApi,
            string tempFolderPath,
            ITempFileManager tempFileManager,
            string agentId,
            ISpeedCalculator speedCalculator,
            ILogger logger,
            BruteforceJob job,
            IHashCatCommandExecutorBuilder executorBuilder)
            : base(job, agentId,
                tempFileManager.BuildTempFilePaths(tempFolderPath),
                executorBuilder,
                krakerApi)
        {
            _logger = logger;
            _tempFileManager = tempFileManager;
            _speedCalculator = speedCalculator;
            _tempFileManager.WriteBase64Content(_paths.HashFile, _job.Content);
            _tempFileManager.WriteBase64Content(_paths.PotFile, _job.PotContent ?? string.Empty);
        }

        public override async Task Finish()
        {
            var result = _hashCatTask.Result;
            if (!result.IsSuccessful)
            {
                await _krakerApi.SendJob(_agentId, _job.JobId,
                    JobResponse.FromError(_job.JobId, result.ExecutionTime, result.ErrorMessage));

                _tempFileManager.DeleteTemFiles(_paths);
                return;
            }

            var err = result.Errors.FirstOrDefault(e =>
                e.Contains("No hashes loaded") || e.Contains("Unhandled Exception"));

            if (err != null)
            {
                await _krakerApi.SendJob(_agentId, _job.JobId, JobResponse.FromError(_job.JobId, result.ExecutionTime, err));
                _tempFileManager.DeleteTemFiles(_paths);
                return;
            }

            var speed = _speedCalculator.CalculateFact(result.Output);
            if (File.Exists(_paths.OutputFile))
            {
                var outfile = Convert.ToBase64String(File.ReadAllBytes(_paths.OutputFile));
                var potfile = Convert.ToBase64String(File.ReadAllBytes(_paths.PotFile));

                await _krakerApi.SendJob(_agentId, _job.JobId,
                    new JobResponse(_job.JobId, outfile, potfile, (long) speed, null, result.ExecutionTime));
            }
            else
            {
                _logger.Information("Output file doesn't exist");
                await _krakerApi.SendJob(_agentId, _job.JobId,
                    new JobResponse(_job.JobId, null, string.Empty, (long) speed, null, result.ExecutionTime));
            }
            _tempFileManager.DeleteTemFiles(_paths);
        }

        public override Task Finish(Exception exception) =>
            _krakerApi.SendJob(_agentId, _job.JobId,
                new JobResponse(_job.JobId, null, string.Empty, 0, exception.Message, 0));
    }
}