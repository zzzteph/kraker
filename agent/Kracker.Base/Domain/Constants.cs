namespace Kracker.Base.Domain
{
    public static class Constants
    {
        public const string ArtefactsFolder = "artefacts";
        public const string AgentInfoFile = "agent-info.json";
        public const string InventoryFile = "inventory.json";
        public const string WordlistsFolder = "wordlist";
        public const string RulesFolder = "rule";
        public const string TempFolder = "tmp";
        public const string AgentIdFile = "agentId";

        public const string HashFile = "hashfile";
        public const string PotFile = "potfile";
        public const string Output = "output";
        
        public static class JobTypes
        {
            public const string SpeedStat = "speedstat";
            public const string HashList = "hashlist";
            public const string TemplateBruteforce = "templatebruteforce";
            public const string TemplateWordlist = "templatewordlist";
            public const string Bruteforce = "bruteforce";
            public const string Wordlist = "wordlist";
            public const string DoNothing = "donothing";
            public const string Incorrect = "incorrect";
        }
        
        public static class WorkStatuses
        {
            public const string Stop = "stop";
            public const string Continue = "continue";
        }
    }
}