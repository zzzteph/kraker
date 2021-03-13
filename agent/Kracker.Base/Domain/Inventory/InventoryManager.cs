using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.Json;
using System.Threading.Tasks;
using Kracker.Base.Domain.AgentId;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Services;
using Kracker.Base.Tools;
using Serilog;
using static Kracker.Base.Domain.Constants;

namespace Kracker.Base.Domain.Inventory
{
    public interface IInventoryManager
    {
        Task Initialize();
        Inventory GetCurrent();
        Task UpdateFileDescriptions();
    }

    public class InventoryManager : IInventoryManager
    {
        private readonly IFileDescriptionBuilder _descriptionBuilder;
        private readonly string _inventoryFilePath;
        private readonly ILogger _logger;
        private readonly WorkedFolders _workedFolders;
        private readonly IAgentIdManager _agentIdManager;

        private Dictionary<string, FileDescription> _fileDescriptions;
        private Inventory _currentInventory;
        private readonly IKrakerApi _krakerApi;

        public InventoryManager(
            AppFolder appFolder,
            ILogger logger,
            IWorkedFoldersProvider workedFoldersProvider,
            IFileDescriptionBuilder descriptionBuilder,
            IKrakerApi krakerApi,
            IAgentIdManager agentIdManager)
        {
            _logger = logger;
            _descriptionBuilder = descriptionBuilder;
            _krakerApi = krakerApi;
            _agentIdManager = agentIdManager;
            _workedFolders = workedFoldersProvider.Get();
            _inventoryFilePath = Path.Combine(appFolder.Value, ArtefactsFolder, InventoryFile);

            _fileDescriptions = new Dictionary<string, FileDescription>();
            _currentInventory = new Inventory(_fileDescriptions.Values);
        }

        public async Task Initialize()
        {
            _fileDescriptions = Directory.GetFiles(_workedFolders.RulesPath)
                    .Concat(Directory.GetFiles(_workedFolders.WordlistPath))
                    .ToDictionary(p => p, _descriptionBuilder.Build);

                File.WriteAllText(_inventoryFilePath, JsonSerializer.Serialize(_fileDescriptions));

                var agentId = GetAgentId();
                var files = await _krakerApi.SendAgentInventory(agentId, _fileDescriptions.Values);
                _currentInventory = new Inventory(files);
        }

        public Inventory GetCurrent() => _currentInventory;

        private string GetAgentId() 
            =>_agentIdManager.GetCurrent().Id ?? throw new InvalidOperationException("The agent needs to have id");


        public async Task UpdateFileDescriptions()
        {
            _logger.Information("[inventory] Time to check inventory!");

            var isChanged = false;
            
            var currentFiles = Directory.GetFiles(_workedFolders.RulesPath)
                .Concat(Directory.GetFiles(_workedFolders.WordlistPath))
                .ToList();

            foreach (var currentFile in currentFiles)
            {
                if (_fileDescriptions.TryGetValue(currentFile, out var oldFileDescription)
                    && File.GetLastWriteTime(currentFile) == oldFileDescription.LastWriteTime)
                    continue;
                try
                {
                    _logger.Information($"[inventory] Have detected a new filе: {currentFile}");
                    _fileDescriptions[currentFile] = _descriptionBuilder.Build(currentFile);
                    isChanged = true;
                }
                catch (Exception e)
                {
                    _logger.Information($"[inventory] can't calculate fileDescription for {currentFile}: {e}");
                }
            }

            var deletedFiles = _fileDescriptions.Keys.ToHashSet();
            deletedFiles.ExceptWith(currentFiles);

            foreach (var deletedFile in deletedFiles)
            {
                _logger.Information($"[inventory] Have detected removing of file: {deletedFile}");
                _fileDescriptions.Remove(deletedFile);
                isChanged = true;
            }

            if (isChanged)
            {
                _logger.Information("[inventory] Changes've detected, save data");
                File.WriteAllText(_inventoryFilePath, JsonSerializer.Serialize(_fileDescriptions));
                
                var agentId = GetAgentId();
                var files = await _krakerApi.SendAgentInventory(agentId, _fileDescriptions.Values);
                _currentInventory = new Inventory(files);
            }
            else
            {
                _logger.Information("[inventory] Checking has finished, changes've not detected");
            }
        }
    }
}