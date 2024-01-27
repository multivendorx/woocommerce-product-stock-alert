import React from 'react';

const Image = (props)=>{
    const style={
        width: '50px',
        height: '50px'
    }
    return(
        <img src={props.src} style={style} />
    )
}

export default Image;