using System;
using System.IO;
using Kracker.Base.Domain.Folders;
using Serilog;

namespace Kracker.Base.Domain.HashCat
{
    public interface ITempFileManager
    {
        TempFilePaths BuildTempFilePaths(string directoryPath);

        void WriteBase64Content(string filePath, string content);

        void DeleteTemFiles(TempFilePaths paths);
    }

    public class TempFileManager : ITempFileManager
    {
        private readonly ILogger _logger;

        public TempFileManager(ILogger logger)
        {
            _logger = logger;
        }

        public TempFilePaths BuildTempFilePaths(string directoryPath) =>
            new (
                BuildTempFilePath(directoryPath),
                BuildTempFilePath(directoryPath),
                BuildTempFilePath(directoryPath)
            );

        public void WriteBase64Content(string filePath, string content)
        {
            File.WriteAllBytes(filePath, Convert.FromBase64String(content));

        }

        private string BuildTempFilePath(string directoryPath)
        {
            string filePath;
            do
            {
                filePath = Path.Combine(directoryPath, Path.GetRandomFileName());
            } while (File.Exists(filePath));

            return filePath;
        }

        
        public void DeleteTemFiles(TempFilePaths paths)
        {
            SoftDelete(paths.HashFile, Constants.HashFile);
            SoftDelete(paths.OutputFile, Constants.Output);
            SoftDelete(paths.PotFile, Constants.PotFile);
        }
        
        private void SoftDelete(string path, string semanticName)
        {
            try
            {
                if (path != null && File.Exists(path))
                    File.Delete(path);
            }
            catch (Exception e)
            {
                _logger.Error($"Can't remove {semanticName}: {path}, {e}");
            }
        }
    }
}