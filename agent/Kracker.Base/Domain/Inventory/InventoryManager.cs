using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.Json;
using System.Threading.Tasks;
using Kracker.Base.Domain.AgentId;
using Kracker.Base.Domain.Folders;
using Kracker.Base.Services;
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
        private readonly IAgentIdManager _agentIdManager;
        private readonly IFileDescriptionBuilder _descriptionBuilder;
        private readonly IKrakerApi _krakerApi;
        private readonly ILogger _logger;
        private readonly WorkedFolders _workedFolders;
        private Inventory _currentInventory;

        private Dictionary<string, FileDescription> _fileDescriptions;

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
            
            _fileDescriptions = new Dictionary<string, FileDescription>();
            _currentInventory = new Inventory(_fileDescriptions.Values);
        }

        public async Task Initialize()
        {
            _fileDescriptions = Directory.GetFiles(_workedFolders.RulesPath)
                .Concat(Directory.GetFiles(_workedFolders.WordlistPath))
                .ToDictionary(p => p, _descriptionBuilder.Build);

            var agentId = GetAgentId();
            var files = await _krakerApi.SendAgentInventory(agentId, _fileDescriptions.Values);
            _currentInventory = new Inventory(files);
        }

        public Inventory GetCurrent()
        {
            return _currentInventory;
        }

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
                
                var agentId = GetAgentId();
                var files = await _krakerApi.SendAgentInventory(agentId, _fileDescriptions.Values);
                _currentInventory = new Inventory(files);
            }
            else
            {
                _logger.Information("[inventory] Checking has finished, changes've not detected");
            }
        }

        private string GetAgentId() => _agentIdManager.GetCurrent().Id ?? throw new InvalidOperationException("The agent needs to have id");
    }
}