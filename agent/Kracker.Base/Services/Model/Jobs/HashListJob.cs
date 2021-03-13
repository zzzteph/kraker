using System.Text.Json.Serialization;
using static Kracker.Base.Domain.Constants.JobTypes;

namespace Kracker.Base.Services.Model.Jobs
{
    public record HashListJob(string HashListId, int HashTypeId, string Content) : AbstractJob(JobType.HashList)
    {
        [JsonPropertyName("hashlist_id")]
        public string HashListId { get; init; } = HashListId;
        
        [JsonPropertyName("hashtype_id")]
        public int HashTypeId { get; init; } = HashTypeId;

        public override JobDescription GetDescription() 
            => new(HashList, HashListId);
    }
}