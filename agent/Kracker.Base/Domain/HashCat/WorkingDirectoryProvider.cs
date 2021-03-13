using System;
using System.IO;
using Kracker.Base.Domain.Configuration;

namespace Kracker.Base.Domain.HashCat
{
    public interface IWorkingDirectoryProvider
    {
        string Get();
        string GetHashCatPath();
    }
    
    public class WorkingDirectoryProvider : IWorkingDirectoryProvider
    {
        private readonly Config _config;
        private readonly string _workingDirectory;

        public WorkingDirectoryProvider(Config config)
        {
            _config = config;
            var hashCatPath = Path.GetDirectoryName(_config.HashCat.Path)
                              ?? throw new InvalidOperationException("There isn't the hashcat path in config");
                
            _workingDirectory = Path.IsPathFullyQualified(_config.HashCat.Path)
                ? hashCatPath
                : Path.Combine(Directory.GetCurrentDirectory(), hashCatPath);
        }

        public string Get() => _workingDirectory;
        public string GetHashCatPath() => Path.Combine(Get(), Path.GetFileName(_config.HashCat.Path));

    }
}