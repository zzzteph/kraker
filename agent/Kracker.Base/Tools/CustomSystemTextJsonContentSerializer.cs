using System.Net.Http;
using System.Reflection;
using System.Text.Json;
using System.Threading;
using System.Threading.Tasks;
using Refit;
using Serilog;

namespace Kracker.Base.Tools
{
    /// <summary>
    ///     Workaround for problem https://github.com/reactiveui/refit/issues/1128
    ///     todo: Remove when the issue will be fixed
    /// </summary>
    public class CustomSystemTextJsonContentSerializer : IHttpContentSerializer
    {
        private readonly SystemTextJsonContentSerializer serializer;

        public CustomSystemTextJsonContentSerializer(JsonSerializerOptions options)
        {
            serializer = new SystemTextJsonContentSerializer(options);
        }


        public HttpContent ToHttpContent<T>(T item) => serializer.ToHttpContent(item);

        public Task<T?> FromHttpContentAsync<T>(HttpContent content, CancellationToken cancellationToken = new())
        {
            if (content.Headers.ContentType != null && content.Headers.ContentType.MediaType == "application/json")
                return serializer.FromHttpContentAsync<T>(content, cancellationToken);
            
            Log.Warning("Got an unexpected result from the server: content type {0}. Check connection and settings is a brilliant idea", content.Headers.ContentType);
            
            return Task.FromResult(default(T));
        }

        public string? GetFieldNameForProperty(PropertyInfo propertyInfo) => serializer.GetFieldNameForProperty(propertyInfo);
    }
}