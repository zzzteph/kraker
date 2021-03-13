using System.Collections.Generic;
using System.Collections.Immutable;
using System.Linq;

namespace Kracker.Base.Domain.Inventory
{
    public record Inventory(FileDescription[] Files)
    {
        public Inventory(IEnumerable<FileDescription> fileDescriptions):this(fileDescriptions.ToArray())
        { }

        public IReadOnlyDictionary<long, FileDescription> Map { get; } = Files.ToDictionary(fd => fd.Id);
    }
}