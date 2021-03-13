using System;
using System.Collections.Generic;
using System.Linq;
using System.Text.Json;
using System.Text.Json.Serialization;

namespace Kracker.Base.Services.Model.Jobs
{
    public class JobConverter : JsonConverter<AbstractJob>
    {
        private readonly IReadOnlyDictionary<JobType, Type> _map = new Dictionary<JobType, Type>
        {
            {JobType.SpeedStat, typeof(SpeedStatJob)},
            {JobType.HashList, typeof(HashListJob)},
            {JobType.TemplateBruteforce, typeof(TemplateBruteforceJob)},
            {JobType.TemplateWordlist, typeof(TemplateWordListJob)},
            {JobType.Bruteforce, typeof(BruteforceJob)},
            {JobType.WordList, typeof(WordListJob)},
            {JobType.DoNothing, typeof(DoNothingJob)}
        };

        public override bool CanConvert(Type typeToConvert)
        {
            return typeToConvert.IsAssignableFrom(typeof(AbstractJob));
        }

        public override AbstractJob? Read(ref Utf8JsonReader reader, Type typeToConvert, JsonSerializerOptions options)
        {
            if (reader.TokenType == JsonTokenType.Null)
                return null;

            var readerInitialCopy = reader;

            using var jsonDocument = JsonDocument.ParseValue(ref reader);
            var root = jsonDocument.RootElement;

            var typeElement = root.EnumerateObject()
                .FirstOrDefault(jp => jp.Name.Equals("type", StringComparison.OrdinalIgnoreCase));

            if (typeElement.Equals(default(JsonProperty)))
                return new IncorrectJob("A job doesn't have a field 'type'");

            var typeAsString = typeElement.Value.GetString();
            if (!Enum.TryParse<JobType>(typeAsString, true, out var jobTypeName))
                return new IncorrectJob($"A job contains the field 'type'= '{typeAsString}'");

            if (!_map.TryGetValue(jobTypeName, out var jobType))
                return new IncorrectJob($"There isn't type for a job type '{nameof(jobTypeName)}'");

            return JsonSerializer.Deserialize(ref readerInitialCopy, jobType, options) as AbstractJob;
        }

        public override void Write(Utf8JsonWriter writer, AbstractJob value, JsonSerializerOptions options)
        {
            JsonSerializer.Serialize(writer, value, value.GetType(), options);
        }
    }
}