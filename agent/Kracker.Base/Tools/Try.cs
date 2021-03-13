using System;
using System.Threading.Tasks;

namespace Kracker.Base.Tools
{
    public static class Try
    {
        public static async Task Do(Func<Task> action, Action<Exception> onError)
        {
            try
            {
                await action();
            }
            catch (Exception e)
            {
                onError(e);
            }
        }
        
        public static async Task<T> Do<T>(Func<Task<T>> action, Func<Exception, T> onError)
        {
            try
            {
                return await action();
            }
            catch (Exception e)
            {
                return onError(e);
            }
        }
    }
}