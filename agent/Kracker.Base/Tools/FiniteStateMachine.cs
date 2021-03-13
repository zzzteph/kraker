using System;
using System.Threading.Tasks;

namespace Kracker.Base.Tools
{
    public class FiniteStateMachine
    {
        private Func<Task> _activeStateAction;

        public FiniteStateMachine(Func<Task> initialAction)
        {
            _activeStateAction = initialAction;
        }

        public void SetStateAction(Func<Task> newAction) => _activeStateAction = newAction;

        public Task RunAction() => _activeStateAction.Invoke();
    }
}