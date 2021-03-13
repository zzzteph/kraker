using System.Text.Json.Serialization;

namespace Kracker.Base.Services.Model.Jobs
{
    [JsonConverter(typeof(JobConverter))]
    public abstract record AbstractJob
    {
        protected AbstractJob(JobType type) => Type = type;
        

        [JsonConverter(typeof(JsonStringEnumConverter))]
        public JobType Type { get; }

        public abstract JobDescription GetDescription();
    }
}