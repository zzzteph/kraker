namespace Kracker.Base.Services.Model.Jobs
{
    public interface IAttackConfiguration
    {
        string Mask { get; }
        string Charset1 { get; }
        string Charset2 { get; }
        string Charset3 { get; }
        string Charset4 { get; }
    }
}