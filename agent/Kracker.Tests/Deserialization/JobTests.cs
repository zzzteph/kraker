using System.Text.Json;
using Kracker.Base.Services.Model.Jobs;
using NUnit.Framework;

namespace Kracker.Tests.Deserialization
{
    public class JobTests
    {
        private static object[] Jobs =
        {
            new object[] {new SpeedStatJob(10)},
            new object[] {new HashListJob("25", 4, "content")},
            new object[] {new TemplateBruteforceJob(33, "mask", "1", "2", "3", "4")},
            new object[] {new TemplateWordListJob(25,3, null)},
            new object[] {new TemplateWordListJob(25,5, 6)},
            new object[] {new BruteforceJob(45, "100", 3, 5, 8,"mask", "1", "2", "3", "4","content", "pot")},
            new object[] {new WordListJob(9, "45", 100, 3, 8, 999, 8238,"content", "pot")}
        };

        [TestCaseSource(nameof(Jobs))]
        public void CorrectObjects(AbstractJob job)
        {
            var processedJob = job.SerializeAndDeserialize();

            Assert.That(processedJob, Is.EqualTo(job));
        }
    }

    public static class ObjectExtensions
    {
        private static readonly JsonSerializerOptions _options = new()
        {
            WriteIndented = true
        };

        public static T SerializeAndDeserialize<T>(this T source)
        {
            var jsonString = JsonSerializer.Serialize(source, _options);

            return JsonSerializer.Deserialize<T>(jsonString, _options);
        }
    }
}