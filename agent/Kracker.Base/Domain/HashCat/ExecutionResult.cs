using System.Collections.Generic;
using System.Linq;

namespace Kracker.Base.Domain.HashCat
{
    public record ExecutionResult(int? HashCatExitCode,
        IReadOnlyList<string> Output,
        IReadOnlyList<string> Errors,
        bool IsSuccessful,
        string? ErrorMessage,
        long ExecutionTime
    )
    {
        public static ExecutionResult FromError(long executionTime, string error) => new (null,
            new string[0],
            new string[0],
            false,
            error, 
            executionTime);
    }
}