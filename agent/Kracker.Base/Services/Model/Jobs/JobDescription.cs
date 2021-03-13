namespace Kracker.Base.Services.Model.Jobs
{
    public record JobDescription(string Type, string Id)
    {
        public JobDescription(string Type, long id): this(Type, $"{id}")
        { }
    }
}