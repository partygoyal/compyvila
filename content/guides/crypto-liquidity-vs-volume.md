Trading volume measures completed trading over a stated period. Liquidity describes how readily an asset can be traded at a given size without substantially moving its price. A market can report high historical volume while offering poor execution for the order you want to place now.

## Activity and capacity answer different questions

Volume is backward-looking: how much changed hands during a window? Liquidity is about available trading conditions: what prices and quantities can you access? A daily activity total cannot guarantee that willing counterparties remain available at this moment.

Before comparing volume figures, record the venue, pair, time window, and units. “One million tokens” and “one million dollars” are different measurements. A token’s global reported volume also does not establish the conditions on your chosen exchange or network.

## Spread, depth, and slippage

The spread is the gap between the best available buying and selling quotes. Depth describes available quantities at different prices. Slippage is the difference between the execution price you expected and the price actually received; market movement and order size can contribute.

Kraken’s [explanation of sliced market orders](https://support.kraken.com/in/articles/sliced-market-orders-explained) describes how orders can consume available levels and receive different prices. Its [market price protection documentation](https://support.kraken.com/articles/201648183-market-price-protection?mode=consumerapp) also explains why low liquidity and volatility can affect market-order execution. Exchange-specific safeguards should not be assumed to work identically elsewhere.

## A hypothetical order-book example

Imagine these are the only immediate sell offers near the current price:

| Tokens offered | Price per token |
| --- | --- |
| 100 | $1.00 |
| 200 | $1.02 |
| 200 | $1.10 |

A market buy for 500 tokens would cost $100 + $204 + $220 = $524 before fees. The average execution price would be $1.048, which is 4.8% above the initial $1 quote. A 50-token order could have a very different result.

These invented offers stay fixed for the example. Real orders can be filled, canceled, or added while a trade is being processed. An order-book screenshot is therefore an observation, not a reservation of those prices.

## How decentralized pools differ

An automated market maker uses pool rules rather than a conventional visible book of limit orders. The relevant question remains how an intended trade affects its execution price.

Uniswap’s [concentrated liquidity documentation](https://docs.uniswap.org/concepts/protocol/concentrated-liquidity) explains that providers can allocate liquidity to chosen price ranges. Positions outside the active range do not provide the same immediate trading capacity at the current price. A large headline pool value can therefore obscure how much liquidity is useful along a particular swap’s price path.

For research, inspect the exact pool, network, token contract, and intended trade size. Compare a quote for the actual route instead of assuming that a token’s largest reported pool determines every transaction.

## A comparison worksheet

For a hypothetical trade, write down the intended size, quoted average price, estimated fees, spread where applicable, and available depth or route quote. Repeat the observation at different times if assessing typical conditions. Label each observation with a timestamp.

Do not add together unrelated liquidity indicators as though they were interchangeable. Daily volume, pool asset value, and depth within 1% of a quoted price measure different things.

A limit order can constrain its permitted price but may remain unfilled. A market order prioritizes immediate execution against available liquidity but can receive a worse price. Neither order type makes an illiquid market liquid.

## Frequently asked questions

### Does high trading volume guarantee low slippage?

No. Completed trades do not reserve present depth for your next order. Execution depends on the market, timing, and order size.

### Is liquidity the same as market cap?

No. Market cap is a price-times-supply calculation. It does not state how many tokens can be bought or sold near that price.

### Does a large pool guarantee a good swap?

No. The active liquidity, selected route, trade size, fees, and changing pool conditions all matter.

## Continue learning

Compare [market cap versus FDV](/read/market-cap-vs-fdv) and [stablecoin depegging](/read/stablecoin-depeg-explained). Liquidity conditions can make both valuation headlines and apparently stable prices harder to interpret.
