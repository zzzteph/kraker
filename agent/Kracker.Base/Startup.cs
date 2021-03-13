using System;
using System.Threading.Tasks;
using Kracker.Base.Domain;
using Kracker.Base.Domain.Configuration;
using Kracker.Base.Domain.HashCat;
using Kracker.Base.Domain.Inventory;
using Kracker.Base.Domain.Jobs;

namespace Kracker.Base
{
    public interface IStartup
    {
        Task<IAgent> PrepareAgent();
    }

    public class Startup : IStartup
    {
        private readonly IConfigValidator _configValidator;
        private readonly IAgentRegistrationManager _registrationManager;
        private readonly IWorkingDirectoryProvider _workingDirectoryProvider;
        private readonly IAgentBuilder _agentBuilder;
        private readonly IInventoryManager _inventoryManager;

        public Startup(
            IConfigValidator configValidator,
            IAgentRegistrationManager registrationManager,
            IWorkingDirectoryProvider workingDirectoryProvider, IAgentBuilder agentBuilder, IInventoryManager inventoryManager)
        {
            _configValidator = configValidator;
            _registrationManager = registrationManager;
            _workingDirectoryProvider = workingDirectoryProvider;
            _agentBuilder = agentBuilder;
            _inventoryManager = inventoryManager;
        }

        public async Task<IAgent> PrepareAgent()
        {
            Environment.CurrentDirectory = _workingDirectoryProvider.Get();
            
            _configValidator.Validate();

            await _registrationManager.Register();
            
            await _inventoryManager.Initialize();

            return _agentBuilder.Build();
        }
    }
}