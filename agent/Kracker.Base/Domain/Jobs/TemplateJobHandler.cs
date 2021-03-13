using System;
using System.Linq;
using System.Threading.Tasks;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Domain.HashCat;
using Kracker.Base.Services;
using Kracker.Base.Services.Model.Jobs;
using Kracker.Base.Services.Model.Responses;

namespace Kracker.Base.Domain.Jobs
{
    public class TemplateJobHandler : JobHandler<TemplateJob>
    {
        public TemplateJobHandler(
            TemplateJob job,
            IKrakerApi krakerApi,
            IHashCatCommandExecutorBuilder executorBuilder,
            string agentId):base(job, agentId, TempFilePaths.Null, executorBuilder, krakerApi)
        { }


        public override async Task Finish()
        {
            var executionResult = _hashCatTask.Result;
            var keyspace = executionResult.Output.LastOrDefault(o => o != null);
            var templateId = _job.TemplateId;

            var keyspaceIsLong = long.TryParse(keyspace, out var keyspaceAsLong);
            await _krakerApi.SendTemplate(_agentId,
                templateId,
                new TemplateResponse(keyspaceIsLong?keyspaceAsLong:0, null, executionResult.ExecutionTime)
            );
        }

        public override Task Finish(Exception exception) 
            => _krakerApi.SendTemplate(_agentId, _job.TemplateId,
            new TemplateResponse(0, exception.Message, 0));
    }
}