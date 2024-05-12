import React from "react";

const Menu: React.FC<{ onStartGame: () => void; }> = (props) => {
    return (
        <>
            <h1>Menu</h1>
            <button onClick={props.onStartGame}>Commencer</button>
        </>
    );
};

export default Menu;