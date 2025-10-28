# Index Archive

**Live at [indexarchive.org](https://indexarchive.org)**

## The Problem

Index funds are fascinating because they're constantly changing. They follow formulas that automatically rebalance
holdings based on market cap, and these shifts tell a story about how global markets evolve.

Most platforms show you the price chart—the aggregate result. I wanted to see under the hood.

## What It Does

Index Archive tracks the historical composition of index funds over time. Instead of just watching a line go up or down,
you can explore:

- **Country allocations** – How market share shifts between nations
- **Sector breakdowns** – Which industries the index favors or reduces
- **Company holdings** – The specific constituents driving the index
- **Additions and removals** - Which companies were added or removed from the index

The goal is to understand not just *how much* the market changed, but *what* changed within it.

## The Challenge

The project started as a tracker for a single index (MSCI World). When I decided to expand it to support multiple
indices, I faced a significant architectural problem. The entire database schema and application structure was built
around one index.

I had to refactor the system from the ground up: redesigning models, reworking relationships, updating queries, and
ensuring the data pipeline could handle the complexity of managing many indices simultaneously.

## How It Works

The data comes from a major international ETF provider's publicly available resources. I built a scraping and processing
pipeline that:

1. Collects holding data
2. Normalizes and stores it in a relational database
3. Surfaces historical comparisons and trends through a web interface

## Tech Stack

Built with:

- **Laravel** – Backend API and business logic
- **Vue.js + Inertia.js** – Frontend SPA experience
- **Tailwind CSS** – UI styling
- **MySQL** – Relational data storage

## Why I Built This

I'm deeply interested in finance and capital markets, and I couldn't find anything out there that tracked index
composition changes over time in an accessible way.

The project pushed me to solve real-world architectural problems—especially around refactoring a system that wasn't
designed for the scale I eventually needed. It's live, functional, and something I'm continuing to develop.

Feature requests and bug reports are welcome!
