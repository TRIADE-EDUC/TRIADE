
export default class {

    contentEntities(diffusionHistory, {_, filter}) {
        return true;
    }

    products(diffusionHistory, {_, filter}) {
        if (! filter.length) {
            return true;
        }

        for (const product of filter) {
            if (!diffusionHistory.diffusion.diffusionProducts.find(p => p.id == product)) {
                return false;
            }
        }
        return true;
    }

    tags(diffusionHistory, {_, filter}) {
        if (! filter.length) {
            return true;
        }
        for (const tag of filter) {
            if (!diffusionHistory.diffusion.tags.find(t => t.id == tag)) {
                return false;
            }
        }
        return true;
    }

    subscribers(diffusionHistory, {_, filter, subscribers}) {
        if (filter) {
            subscribers = subscribers.filter(value => value.toLowerCase().includes(filter.toLowerCase()))
            return subscribers.length > 0;
        }
        return true;
    }

    channel(diffusionHistory, {_, filter, contentHistory}) {
        if (filter.length && contentHistory) {
            return filter.includes(contentHistory.type);
        }
        return true;
    }

    entities(diffusionHistory, {_, filter, entitiesType}) {
        for (const entityType of filter) {
            if (!entitiesType.includes(entityType)) {
                return false;
            }
        }
        return true;
    }

    diffusions(diffusionHistory, {_, filter}) {
        const name = JSON.stringify(diffusionHistory.diffusion.name).toLowerCase();
        return name.includes(filter.toLowerCase());
    }

    date(diffusionHistory, {_, filter}) {
        const invalidDate = "Invalid Date";

        let historyDate = new Date(diffusionHistory.date);

        let start = new Date(filter.start);
        start.setHours(0, 0, 0, 0);

        let end = new Date(filter.end);
        end.setHours(23, 59, 59, 999);

        if(historyDate == invalidDate || start == invalidDate || end == invalidDate) {
            return true;
        }
        return historyDate >= start && historyDate <= end;
    }
}
