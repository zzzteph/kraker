using System;
using System.IO;
using System.Linq;
using System.Security.Cryptography;

namespace Kracker.Base.Domain.Inventory
{
    public interface IFileDescriptionBuilder
    {
        FileDescription Build(string filePath);
    }

    public class FileDescriptionBuilder : IFileDescriptionBuilder
    {
        public FileDescription Build(string filePath)
        {
            var fileInfo = new FileInfo(filePath);
            var fd = new FileDescription
            {
                Name = fileInfo.Name,
                Size = fileInfo.Length,
                FolderName = fileInfo.Directory?.Name ?? String.Empty,
                LastWriteTime = fileInfo.LastWriteTime
            };

            using (var md5 = MD5.Create())
            using (var reader = fileInfo.OpenRead())
            {
                var hash = md5.ComputeHash(reader);
                fd.Сhecksum = string.Concat(hash.Select(b => b.ToString("X2")));
            }

            using (var reader = fileInfo.OpenText())
            {
                while (reader.ReadLine() != null)
                    fd.LinesCount++;
            }

            return fd;
        }
    }
}