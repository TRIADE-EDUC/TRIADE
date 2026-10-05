import Filters from "./Filters.js";

const CONTENT_TYPES_SUBSCRIBER = 1;
const CONTENT_TYPES_ITEM = 2;
const CONTENT_TYPES_VIEW = 3;
const CONTENT_TYPES_RENDER_VIEW = 4;
const CONTENT_TYPES_CHANNEL = 5;

export default class extends Filters {

    subscribers(diffusionHistory, options) {
        let subscribers = getAllSubscribers(diffusionHistory) ?? [];
        return super.subscribers(diffusionHistory, {...options, subscribers});
    }

    channel(diffusionHistory, {_, filter, contentHistoryTypes}) {
        const contentHistory = getContentFromContentHistory(diffusionHistory, contentHistoryTypes['channel']);
        return super.channel(diffusionHistory, {_, filter, contentHistory});
    }

    entities(diffusionHistory, options) {
        const entitiesType = getAllItemType(diffusionHistory) ?? [];
        return super.entities(diffusionHistory, {...options, entitiesType});
    }
}

function getAllSubscribers(diffusionHistory) {
    let subscribers = [];

    const contentBuffer = diffusionHistory.contentBuffer[CONTENT_TYPES_SUBSCRIBER] ?? [];
    if (contentBuffer.length) {
        for (const contentChild of contentBuffer) {
            contentChild.content.forEach(subscriber => {
                subscribers.push(subscriber.name);
                Object.values(subscriber.settings).forEach(value => subscribers.push(value));
            });
        }
    }

    const contentHistory = diffusionHistory.contentHistory[CONTENT_TYPES_SUBSCRIBER]  ?? [];
    if (contentHistory.length) {
        for (const contentChild of contentHistory) {
            contentChild.content.forEach(subscriber => {
                subscribers.push(subscriber.name);
                Object.values(subscriber.settings).forEach(value => subscribers.push(value));
            });
        }
    }

    return [...new Set(subscribers)];
}

function getAllItemType(diffusionHistory) {
    let types = [];

    const contentBuffer = diffusionHistory.contentBuffer[CONTENT_TYPES_ITEM] ?? [];
    if (contentBuffer.length) {
        for (const contentChild of contentBuffer) {
            types.push(contentChild.content.type);
        }
    }

    const contentHistory = diffusionHistory.contentHistory[CONTENT_TYPES_ITEM]  ?? [];
    if (contentHistory.length) {
        for (const contentChild of contentHistory) {
            types.push(contentChild.content.type);
        }
    }
    return [...new Set(types)];
}

function getContentHistory(history, contentHistoryType) {
    if (history.contentHistory[contentHistoryType] && history.contentHistory[contentHistoryType][0]) {
        const contentHistory = history.contentHistory[contentHistoryType][0][0];
        return contentHistory || null;
    }
    return null;
}

function getContentFromContentHistory(history, contentHistoryType) {
    const contentHistory = getContentHistory(history, contentHistoryType);
    return contentHistory ? contentHistory.content : null;
}