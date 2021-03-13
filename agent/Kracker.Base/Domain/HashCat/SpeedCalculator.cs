using System;
using System.Collections.Generic;
using System.Linq;
using System.Numerics;
using Serilog;

namespace Kracker.Base.Domain.HashCat
{
    public interface ISpeedCalculator
    {
        BigInteger CalculateBenchmark(IReadOnlyList<string> lines);
        double CalculateFact(IReadOnlyList<string> hashcatOut);
    }

    public class SpeedCalculator : ISpeedCalculator
    {
        private readonly ILogger _logger;

        public SpeedCalculator(ILogger logger)
        {
            _logger = logger;
        }

        public BigInteger CalculateBenchmark(IReadOnlyList<string> lines)
        {
            return lines.Select(l => l.Split(new[] {':'}, StringSplitOptions.RemoveEmptyEntries))
                .Where(la => la.Length == 6)
                .Select(la => BigInteger.Parse(la[5])).Aggregate(BigInteger.Zero, (abi, bi) => abi + bi);
        }

        public double CalculateFact(IReadOnlyList<string> hashcatOut)
        {
            if (hashcatOut == null || hashcatOut.Count == 0)
                return 0d;

            var speeds = new List<double>();
            foreach (var t in hashcatOut)
                try
                {
                    var outStr = t;
                    var start = outStr.IndexOf("SPEED	");
                    var end = outStr.IndexOf("EXEC_RUNTIME");
                    var speed = outStr.Substring(start + 6, end - start - 6)
                        .Split(new[] {"	"}, StringSplitOptions.RemoveEmptyEntries)
                        .Select(double.Parse).ToArray();

                    var cs = 0d;
                    for (var i = 0; i < speed.Length / 2; i++)
                        cs += speed[2 * i] * 1000 / speed[2 * i + 1];

                    speeds.Add(cs);
                }
                catch (Exception e)
                {
                    _logger.Warning($"[Speed] Can't calculate speed: {t}. {e}");
                }

            return speeds.Any() ? speeds.Average() : 0;
        }
    }
}