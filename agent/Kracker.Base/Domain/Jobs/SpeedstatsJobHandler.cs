using System;
using System.Threading.Tasks;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Domain.HashCat;
using Kracker.Base.Services;
using Kracker.Base.Services.Model.Jobs;
using Kracker.Base.Services.Model.Responses;

namespace Kracker.Base.Domain.Jobs
{
    public class SpeedstatsJobHandler : JobHandler<SpeedStatJob>
    {
        private readonly ISpeedCalculator _speedCalculator;

        public SpeedstatsJobHandler(
            IKrakerApi krakerApi,
            ISpeedCalculator speedCalculator,
            string agentId,
            IHashCatCommandExecutorBuilder executorBuilder,
            SpeedStatJob job) : base(job, agentId, TempFilePaths.Null, executorBuilder, krakerApi)
        {
            _speedCalculator = speedCalculator;
        }

        public override async Task Finish()
        {
            var executionResult = _hashCatTask.Result;
            var speed = _speedCalculator.CalculateBenchmark(executionResult.Output);

            var hashTypeId = _job.HashTypeId;
            var stat = new SpeedStatResponse(hashTypeId, speed.ToString(), executionResult.ExecutionTime);

            await _krakerApi.SendSpeedStats(_agentId,
                stat);
        }

        public override Task Finish(Exception exception)
            => _krakerApi.SendSpeedStats(_agentId, new SpeedStatResponse(_job.HashTypeId, "0", 0));

    }
}