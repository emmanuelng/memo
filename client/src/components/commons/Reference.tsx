import React from "react";

const Reference: React.FC<{ data: any; }> = (props) => {
    const book: string = props.data.book;
    const firstVerse: any = props.data.firstVerse;
    const lastVerse: any | null = props.data.lastVerse;

    let referenceStr = `${book} `;

    if (!lastVerse) {
        referenceStr += `${firstVerse.chapter}:${firstVerse.verse}`;
    } else if (firstVerse.chapter === lastVerse.chapter) {
        referenceStr += firstVerse.verse === lastVerse.verse
            ? `${firstVerse.chapter}:${firstVerse.verse}`
            : `${firstVerse.chapter}:${firstVerse.verse}-${lastVerse.verse}`;
    } else {
        referenceStr += `${firstVerse.chapter}:${firstVerse.verse}`;
        referenceStr += ' - ';
        referenceStr += `${lastVerse.chapter}:${lastVerse.verse}`;
    }

    return <span id="reference">{referenceStr}</span>;
};

export default Reference;
