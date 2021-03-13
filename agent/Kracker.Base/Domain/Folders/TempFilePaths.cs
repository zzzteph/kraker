namespace Kracker.Base.Domain.Folders
{
    public record TempFilePaths(string PotFile,
        string HashFile,
        string OutputFile)
    {
        public static TempFilePaths Null { get; } = new(string.Empty, string.Empty, string.Empty);
    };
}