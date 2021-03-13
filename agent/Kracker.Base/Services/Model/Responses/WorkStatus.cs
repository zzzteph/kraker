using Kracker.Base.Domain;

namespace Kracker.Base.Services.Model.Responses
{
    public record WorkStatus(string Status)
    {
        public static WorkStatus Continue => new (Constants.WorkStatuses.Continue);
    }
}