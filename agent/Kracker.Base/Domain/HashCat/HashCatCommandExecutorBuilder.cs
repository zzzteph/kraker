using Kracker.Base.Domain.Configuration;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Services.Model.Jobs;
using Serilog;

namespace Kracker.Base.Domain.HashCat
{
    public interface IHashCatCommandExecutorBuilder
    {
        HashCatCommandExecutor Build(AbstractJob job, TempFilePaths paths);
        HashCatCommandExecutor Build(string arguments);
    }
    
    public class HashCatCommandExecutorBuilder : IHashCatCommandExecutorBuilder
    {
        private readonly string _workingDirectoty;
        private readonly IArgumentsBuilder _argumentsBuilder;
        private readonly HashCatSettings _settings;
        private readonly ILogger _logger;
        
        public HashCatCommandExecutorBuilder(IWorkingDirectoryProvider workingDirectoryProvider,
            IArgumentsBuilder argumentsBuilder,
            Config config, 
            ILogger logger)
        {
            _argumentsBuilder = argumentsBuilder;
            _logger = logger;
            _workingDirectoty = workingDirectoryProvider.Get();
            _settings = config.HashCat;
        }
        
        public HashCatCommandExecutor Build(AbstractJob job, TempFilePaths paths)
        {
            var arguments = _argumentsBuilder.Build(job, paths);
            return new (arguments, _settings, _logger, _workingDirectoty, job);
        }

        public HashCatCommandExecutor Build(string arguments)
            => new (arguments, _settings, _logger, _workingDirectoty, null);
    }
}