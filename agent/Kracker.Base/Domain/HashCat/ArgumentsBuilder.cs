using System;
using System.IO;
using Kracker.Base.Domain.Configuration;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Domain.Inventory;
using Kracker.Base.Services.Model.Jobs;

namespace Kracker.Base.Domain.HashCat
{
    public interface IArgumentsBuilder
    {
        string Build(AbstractJob job, TempFilePaths paths);
    }

    public class ArgumentsBuilder : IArgumentsBuilder
    {
        private readonly string _force;
        private readonly IInventoryManager _inventoryManager;
        private readonly string _options;
        private readonly WorkedFolders _workedFolders;

        public ArgumentsBuilder(IWorkedFoldersProvider workedFoldersProvider,
            Config config,
            IInventoryManager inventoryManager)
        {
            _inventoryManager = inventoryManager;
            _workedFolders = workedFoldersProvider.Get();
            _options = config.HashCat.Options;
            _force = config.HashCat.NeedForce
                ? "--force --self-test-disable "
                : string.Empty;
        }

        public string Build(AbstractJob job, TempFilePaths paths)
        {
            var inventory = _inventoryManager.GetCurrent();

            return job switch
            {
                SpeedStatJob ssj => $"{_force}-b -m {ssj.HashTypeId} --machine-readable",
                TemplateBruteforceJob tmj => $"{_force}{_options} --keyspace -a 3 "
                                             + BuildAttackConfiguration(tmj),

                TemplateWordListJob twl => $"{_force}{_options} --keyspace {BuildRule(twl.RuleId, inventory)}"
                                           + $" \"{Path.Combine(_workedFolders.WordlistPath, inventory.Map[twl.WordlistId].Name)}\"",

                HashListJob hlj => $"{_force}--left -m {hlj.HashTypeId} {BuildFilePaths(paths)}",

                BruteforceJob bfj => $"{_force}{_options} --skip={bfj.Skip} --limit={bfj.Limit} -m {bfj.HashTypeId} "
                                     + $" --outfile=\"{paths.OutputFile}\" "
                                     + $"{BuildFilePaths(paths)} -a 3 "
                                     + BuildAttackConfiguration(bfj),

                WordListJob wlj => $"{_force}{_options} --skip={wlj.Skip} --limit={wlj.Limit} -m {wlj.HashTypeId} "
                                   + BuildRule(wlj.RuleId, inventory)
                                   + $" --outfile=\"{paths.OutputFile}\" " +
                                   $"{BuildFilePaths(paths)} \"{Path.Combine(_workedFolders.WordlistPath, inventory.Map[wlj.WordlistId].Name)}\"",

                _ => throw new InvalidOperationException($"Can't build hascat arguments for {job}")
            };
        }

        private string BuildAttackConfiguration(IAttackConfiguration configuration) =>
            (configuration.Charset1 is null ? string.Empty : $"-1 {configuration.Charset1} ")
            + (configuration.Charset2 is null ? string.Empty : $"-2 {configuration.Charset2} ")
            + (configuration.Charset3 is null ? string.Empty : $"-3 {configuration.Charset3} ")
            + (configuration.Charset4 is null ? string.Empty : $"-4 {configuration.Charset4} ")
            + configuration.Mask;

        private string BuildRule(long? ruleId, Inventory.Inventory inventory) =>
            ruleId.HasValue
                ? $"-r \"{Path.Combine(_workedFolders.RulesPath, inventory.Map[ruleId.Value].Name)}\""
                : string.Empty;

        private string BuildFilePaths(TempFilePaths paths) =>
            paths.PotFile == null
                ? $" \"{paths.HashFile}\" "
                : $"--potfile-path=\"{paths.PotFile}\" \"{paths.HashFile}\" ";
    }
}