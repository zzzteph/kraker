using System;
using Kracker.Base.Domain.Configuration;
using Microsoft.Extensions.DependencyInjection;

namespace Kracker.Base.Injection
{
    public static class ServiceProviderBuilder
    {
        public static IServiceProvider Build(Config config) =>
            new ServiceCollection()
                .RegisterAppDirectory()
                .RegisterConfig(config)
                .RegisterLogging()
                .RegisterKrakerApi(config)
                .RegisterAllTypesAsSingleton()
                .BuildServiceProvider();
    }
}