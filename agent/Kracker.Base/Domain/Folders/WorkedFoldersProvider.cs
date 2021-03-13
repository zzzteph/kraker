namespace Kracker.Base.Domain.Folders
{
    public interface IWorkedFoldersProvider
    {
        WorkedFolders Get();
    }

    public class WorkedFoldersProvider : IWorkedFoldersProvider
    {
        private readonly WorkedFolders _workedFolders;

        public WorkedFoldersProvider(IWorkedFoldersManager workedFoldersManager)
        {
            _workedFolders = workedFoldersManager.Prepare();
        }

        public WorkedFolders Get() => _workedFolders;
    }
}