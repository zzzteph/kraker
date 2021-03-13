using System.IO;
using static Kracker.Base.Domain.Constants;

namespace Kracker.Base.Domain.Folders
{
    public interface IWorkedFoldersManager
    {
        WorkedFolders Prepare();
    }

    public class WorkedFoldersManager : IWorkedFoldersManager
    {
        private readonly string _appDirectory;

        public WorkedFoldersManager(AppFolder appFolder)
        {
            _appDirectory = appFolder.Value;
        }

        public WorkedFolders Prepare()
        {
            var workedDirectories = new WorkedFolders(Path.Combine(_appDirectory, WordlistsFolder),
                Path.Combine(_appDirectory, RulesFolder),
                Path.Combine(_appDirectory, ArtefactsFolder, TempFolder));

            if (!Directory.Exists(workedDirectories.WordlistPath))
                Directory.CreateDirectory(workedDirectories.WordlistPath);

            if (!Directory.Exists(workedDirectories.RulesPath))
                Directory.CreateDirectory(workedDirectories.RulesPath);

            Directory.CreateDirectory(workedDirectories.TempFolderPath);
            return workedDirectories;
        }
    }
}