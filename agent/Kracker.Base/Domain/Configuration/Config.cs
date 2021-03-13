namespace Kracker.Base.Domain.Configuration
{
    public record Config
    {
        public HashCatSettings HashCat { get; set; }
        public string ServerUrl { get; set; }
        public int? InventoryCheckPeriod { get; set; }
        public int? HearbeatPeriod { get; set; }
    }

    public record HashCatSettings
    {
        public int? SilencePeriodBeforeKill { get; set; }
        public int? RepeatedStringsBeforeKill { get; set; }
        public string Path { get; set; } = string.Empty;
        public string Options { get; set; } = string.Empty;
        
        public bool NeedForce { get; set; }
    }
}