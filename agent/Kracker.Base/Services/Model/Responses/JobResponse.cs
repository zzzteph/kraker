namespace Kracker.Base.Services.Model.Responses
{
    public record JobResponse(
        long JobId,
        string? Outfile, //base64
        string? Potfile, //base64
        long Speed,
        string? Error,
        long Time)
    {
        public static JobResponse FromError(long jobId, long time, string? error)
            => new(jobId, null, null, 0, error, time);
    }
}